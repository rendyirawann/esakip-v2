<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipUrusan;

/**
 * SakipUrusanSearch represents the model behind the search form of `backend\models\SakipUrusan`.
 */
class SakipUrusanSearch extends SakipUrusan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['urusan_id'], 'integer'],
            [['kode_urusan', 'nama_urusan', 'urusan_isaktif'], 'safe'],
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
        $query = SakipUrusan::find();

        // add conditions that should always apply here

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
            'urusan_id' => $this->urusan_id,
        ]);

        $query->andFilterWhere(['like', 'kode_urusan', $this->kode_urusan])
            ->andFilterWhere(['like', 'nama_urusan', $this->nama_urusan])
            ->andFilterWhere(['like', 'urusan_isaktif', $this->urusan_isaktif]);

        return $dataProvider;
    }
}
