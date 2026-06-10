<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipVisiP;

/**
 * SakipVisiSearch represents the model behind the search form of `frontend\models\SakipVisi`.
 */
class SakipVisiPSearch extends SakipVisiP
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refvisi_p_id', 'refperiode_5tahun_id'], 'integer'],
            [['uraian_visi_p', 'penjabaran_visi_p', 'visi_p_isaktif'], 'safe'],
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
        $query = SakipVisiP::find();

        // Eager load relations for performance
        $query->with(['periode5Tahun']);

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
            'refvisi_p_id' => $this->refvisi_p_id,
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_visi_p', $this->uraian_visi_p])
            ->andFilterWhere(['like', 'penjabaran_visi_p', $this->penjabaran_visi_p])
            ->andFilterWhere(['like', 'visi_p_isaktif', $this->visi_p_isaktif]);

        return $dataProvider;
    }
}
