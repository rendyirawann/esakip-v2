<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipMisiP;

/**
 * SakipMisiSearch represents the model behind the search form of `backend\models\SakipMisi`.
 */
class SakipMisiPSearch extends SakipMisiP
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refmisi_p_id', 'refperiode_5tahun_id', 'refvisi_p_id'], 'integer'],
            [['uraian_misi_p', 'misi_p_isaktif'], 'safe'],
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
        $query = SakipMisiP::find();

        // add conditions that should always apply here
        $query->with(['periode', 'visi']);

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
            'refmisi_p_id' => $this->refmisi_p_id,
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
            'refvisi_p_id' => $this->refvisi_p_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_misi_p', $this->uraian_misi_p])
            ->andFilterWhere(['like', 'misi_p_isaktif', $this->misi_p_isaktif]);

        return $dataProvider;
    }
}
