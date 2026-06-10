<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipBidang;

/**
 * SakipBidangSearch represents the model behind the search form of `backend\models\SakipBidang`.
 */
class SakipBidangSearch extends SakipBidang
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refbidang_id', 'refurusan_id'], 'integer'],
            [['kode_bidang', 'nama_bidang', 'bidang_isaktif'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SakipBidang::find();

        // add conditions that should always apply here
        $query->with(['urusan']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refbidang_id' => $this->refbidang_id,
            'refurusan_id' => $this->refurusan_id,
        ]);

        $query->andFilterWhere(['like', 'kode_bidang', $this->kode_bidang])
            ->andFilterWhere(['like', 'nama_bidang', $this->nama_bidang])
            ->andFilterWhere(['like', 'bidang_isaktif', $this->bidang_isaktif]);

        return $dataProvider;
    }
}
